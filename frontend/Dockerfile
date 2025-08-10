FROM node:24 AS base

ENV NEXT_TELEMETRY_DISABLED=1

WORKDIR /app

RUN corepack enable pnpm && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store

EXPOSE 3000
EXPOSE 5173


FROM base AS prod

WORKDIR /app

ENV PORT=3000
ENV HOSTNAME="0.0.0.0"
ENV NODE_ENV=production
ENV NEXT_PUBLIC_BACKEND_URL="https://backend.fantasy-academy.com"

COPY --link pnpm-lock.yaml ./
RUN pnpm fetch

COPY --link . .

RUN pnpm install --frozen-lockfile && \
    pnpm run build

CMD ["node", "server.js"]
