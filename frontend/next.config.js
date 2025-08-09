const isProd = process.env.NODE_ENV === 'production';

/** @type {import("next").NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  output: "standalone",
  images: {
    minimumCacheTTL: 3600,
    remotePatterns: [
      {
        protocol: "https",
        hostname: "covers.openlibrary.org",
        port: "",
        pathname: "/b/id/**"
      }
    ]
  },
    typescript: {
      ignoreBuildErrors: isProd
    }
}

module.exports = nextConfig;
