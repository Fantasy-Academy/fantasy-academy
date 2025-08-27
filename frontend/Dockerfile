FROM node:24 AS base

WORKDIR /app

RUN corepack enable pnpm && \
	corepack prepare --activate pnpm@latest && \
	pnpm config -g set store-dir /.pnpm-store



FROM base AS build

ENV NODE_ENV=production
ENV VITE_API_BASE_URL="https://fantasy-academy.com"
ENV VITE_API_DEBUG=false

COPY --link pnpm-lock.yaml package.json ./
RUN pnpm fetch

COPY --link . .
RUN pnpm install --frozen-lockfile && pnpm run build



FROM node:24-alpine AS runner

ENV NODE_ENV=production
ENV VITE_API_BASE_URL="https://fantasy-academy.com"
ENV VITE_API_DEBUG=false
ENV PORT=3000

WORKDIR /app

RUN npm install -g serve

# copy the built static files
COPY --from=build /app/dist ./dist

EXPOSE 3000
CMD ["serve", "-s", "dist", "-l", "3000"]
