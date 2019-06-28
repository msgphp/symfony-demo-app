ARG image_app
ARG image_phpqa

FROM ${image_phpqa} AS toolbox
FROM ${image_app} AS app_test

COPY --from=toolbox /tools/php-cs-fixer /tools/
COPY --from=toolbox /tools/psalm /tools/
ENV PATH "${PATH}:/tools"
