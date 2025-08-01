FROM node:24 AS base

ENV NEXT_TELEMETRY_DISABLED=1

WORKDIR /app

RUN corepack enable pnpm && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store

EXPOSE 3000
EXPOSE 5173


FROM base AS builder

COPY --link pnpm-lock.yaml ./
RUN pnpm fetch

COPY --link . .

RUN pnpm install --frozen-lockfile && \
    pnpm run build


# Production image, copy all the files and run next
FROM node_upstream AS prod

WORKDIR /app

ENV NODE_ENV=production

COPY --from=builder --link /app/public ./public

RUN mkdir .next

# Automatically leverage output traces to reduce image size
# https://nextjs.org/docs/advanced-features/output-file-tracing
COPY --from=builder --link /app/.next/standalone ./
COPY --from=builder --link /app/.next/static ./.next/static

EXPOSE 3000

ENV PORT=3000
ENV HOSTNAME="0.0.0.0"

CMD ["node", "server.js"]
