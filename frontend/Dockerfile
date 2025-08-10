FROM node:24 AS base

ENV NEXT_TELEMETRY_DISABLED=1

WORKDIR /app

RUN corepack enable pnpm && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store



FROM base AS build

ENV NODE_ENV=production
ENV NEXT_PUBLIC_BACKEND_URL="https://backend.fantasy-academy.com"

COPY --link pnpm-lock.yaml package.json ./
RUN pnpm fetch

COPY --link . .
RUN pnpm install --frozen-lockfile && pnpm run build



FROM node:24 AS runner

ENV NODE_ENV=production
ENV PORT=3000
ENV HOSTNAME="0.0.0.0"

WORKDIR /app

# copy the minimal standalone runtime
COPY --from=build /app/.next/standalone ./
COPY --from=build /app/.next/static ./.next/static
COPY --from=build /app/public ./public

EXPOSE 3000
CMD ["node", "server.js"]
