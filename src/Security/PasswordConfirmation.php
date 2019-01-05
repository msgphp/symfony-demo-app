<?php

declare(strict_types=1);

namespace App\Security;

use MsgPhp\User\Infra\Form\Type\HashedPasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class PasswordConfirmation
{
    private $secret;
    private $twig;
    private $formFactory;
    private $urlGenerator;

    public function __construct(string $secret, Environment $twig, FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator)
    {
        $this->secret = $secret;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function confirm(Request $request): ?Response
    {
        $session = $request->getSession();

        if (null === $session) {
            throw new \LogicException('Session not available.');
        }

        $hash = md5(implode("\0", [
            $this->secret,
            $request->getClientIp(),
            $request->getUriForPath($request->getRequestUri()),
        ]));

        if ($session->has($hash)) {
            if ($session->remove($hash) !== md5($hash.$this->secret)) {
                throw new BadRequestHttpException('Unable to confirm current request.');
            }

            return null;
        }

        $referer = (null !== $route = $request->attributes->get('_route'))
            ? $this->urlGenerator->generate($route, $request->attributes->get('_route_params', []), UrlGeneratorInterface::ABSOLUTE_URL)
            : $request->headers->get('referer');

        $form = $this->formFactory->createNamedBuilder($hash)
            ->add('currentPassword', HashedPasswordType::class, [
                'password_confirm_current' => true,
            ])
            ->add('referer', HiddenType::class, [
                'data' => $referer,
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $session->set($hash, md5($hash.$this->secret));

                return new RedirectResponse($request->getRequestUri());
            }

            $referer = $form->get('referer')->getData();
        }

        if (null === $referer || $hash === md5($referer)) {
            throw new BadRequestHttpException('Unable to confirm current request.');
        }

        return new Response($this->twig->render('password_confirmation.html.twig', [
            'form' => $form->createView(),
            'cancelUrl' => $referer,
        ]));
    }
}
