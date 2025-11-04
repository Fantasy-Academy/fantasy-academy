FROM node:24 AS build

WORKDIR /app

RUN corepack enable pnpm && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store

ENV NODE_ENV=production
ENV VITE_API_BASE_URL="https://backend.fantasy-academy.com"
ENV VITE_API_DEBUG=false

ARG VITE_SENTRY_DSN
ENV VITE_SENTRY_DSN="${VITE_SENTRY_DSN}"

ARG VITE_APP_VERSION
ENV VITE_APP_VERSION="${VITE_APP_VERSION}"

ARG SENTRY_AUTH_TOKEN
ENV SENTRY_AUTH_TOKEN="${SENTRY_AUTH_TOKEN}"

COPY --link pnpm-lock.yaml package.json ./
RUN pnpm fetch

COPY --link . .
RUN pnpm install --frozen-lockfile && pnpm run build



FROM nginx:alpine AS runner

# Copy built static files
COPY --from=build /app/dist /usr/share/nginx/html

# Copy nginx configuration for SPA routing
COPY .deploy/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
