/** @type {import('next').NextConfig} */
const nextConfig = {
  allowedDevOrigins: ['127.0.0.1:3000', 'localhost:3000'],
  images: {
    remotePatterns: [
      {
        // Local PHP backend dev server
        protocol: 'http',
        hostname: '127.0.0.1',
        port: '8000',
        pathname: '/images/**',
      },
      {
        // Production backend
        protocol: 'https',
        hostname: 'backend.sdcolourslab.in',
        pathname: '/images/**',
      },
    ],
  },
};

export default nextConfig;

