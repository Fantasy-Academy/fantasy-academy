FROM node:24 AS dev

WORKDIR /app

RUN corepack enable pnpm && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store


FROM dev AS build

ENV NODE_ENV=production
ENV VITE_API_BASE_URL="https://backend.fantasy-academy.com"
ENV VITE_API_DEBUG=false

ARG VITE_SENTRY_DSN
ENV VITE_SENTRY_DSN="${VITE_SENTRY_DSN}"

ARG VITE_APP_VERSION
ENV VITE_APP_VERSION="${VITE_APP_VERSION}"

COPY --link pnpm-lock.yaml package.json ./
RUN pnpm fetch

COPY --link . .

# SENTRY_AUTH_TOKEN arrives as a BuildKit SECRET, never as a build ARG.
#
# vite.config.ts enables sentryVitePlugin (sourcemap upload) only when
# process.env.SENTRY_AUTH_TOKEN is set, so the token has to exist for this one
# command — but it must NOT be baked in. It used to be `ARG SENTRY_AUTH_TOKEN` +
# `ENV`, which is unsafe here: the shared release workflow
# (TheDevs-cz/ci/_build.yml) emits an SLSA provenance attestation by default, and
# build args are recorded in it — publishing the token alongside the image.
# A secret mount is scoped to this RUN, is never written to a layer, and is not
# recorded in provenance. `required=false` keeps a plain `docker build` working
# locally (the build then just skips the upload).
RUN --mount=type=secret,id=SENTRY_AUTH_TOKEN,env=SENTRY_AUTH_TOKEN,required=false \
    pnpm install --frozen-lockfile && pnpm run build



FROM nginx:alpine AS runner

# Copy built static files
COPY --from=build /app/dist /usr/share/nginx/html

# Copy nginx configuration for SPA routing
COPY .deploy/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
