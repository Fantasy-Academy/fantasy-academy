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
      },
      {
        protocol: "https",
        hostname: "placecats.com",
        port: "",
        pathname: "/**"
      },
      {
        protocol: "https",
        hostname: "img.fantasy-academy.com",
        port: "",
        pathname: "/**"
      }
    ]
  },
  typescript: {
    ignoreBuildErrors: isProd
  }
};

module.exports = nextConfig;
