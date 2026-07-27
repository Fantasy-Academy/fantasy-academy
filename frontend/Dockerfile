FROM node:24 AS dev

WORKDIR /app

RUN corepack enable pnpm && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store


FROM dev AS build

ENV NODE_ENV=production
ENV VITE_API_BASE_URL="https://backend.fantasy-academy.com"
ENV VITE_API_DEBUG=false

ARG VITE_APP_VERSION
ENV VITE_APP_VERSION="${VITE_APP_VERSION}"

COPY --link pnpm-lock.yaml package.json ./
RUN pnpm fetch

COPY --link . .

# Both Sentry values arrive as BuildKit SECRET MOUNTS, not build ARGs. Vite reads
# VITE_*/SENTRY_* from process.env, and a secret mount's `env=` gives exactly that
# for the duration of this one RUN — so the values reach the build without being
# baked into a layer or into image metadata.
#
#   SENTRY_AUTH_TOKEN — a real secret. vite.config.ts enables sentryVitePlugin
#     (sourcemap upload) only when process.env.SENTRY_AUTH_TOKEN is set. It used to
#     be `ARG` + `ENV`, which is unsafe here: the shared release workflow
#     (TheDevs-cz/ci/_build.yml) emits an SLSA provenance attestation by default and
#     build args are recorded in it — publishing the token alongside the image.
#   VITE_SENTRY_DSN — NOT secret (Vite compiles it into the public bundle, which is
#     the point). It is a mount purely for a CI-plumbing reason: the value lives in a
#     repo secret, and GitHub forbids the `secrets` context in a reusable workflow's
#     `with:` block, so it cannot travel as a build arg. See frontend-release.yml.
#
# required=false on both keeps a plain local `docker build` working: no mounts means
# no sourcemap upload and an empty DSN (Sentry simply stays disabled).
RUN --mount=type=secret,id=SENTRY_AUTH_TOKEN,env=SENTRY_AUTH_TOKEN,required=false \
    --mount=type=secret,id=VITE_SENTRY_DSN,env=VITE_SENTRY_DSN,required=false \
    pnpm install --frozen-lockfile && pnpm run build



FROM nginx:alpine AS runner

# Copy built static files
COPY --from=build /app/dist /usr/share/nginx/html

# Copy nginx configuration for SPA routing
COPY .deploy/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
