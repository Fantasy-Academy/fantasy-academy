// pages/api/auth/[...nextauth].ts
import NextAuth from "next-auth";
import CredentialsProvider from "next-auth/providers/credentials";
import type { NextAuthOptions } from "next-auth";

const API_BASE = (
  process.env.BACKEND_URL ||
  process.env.NEXT_PUBLIC_BACKEND_URL ||
  "http://localhost:8080"
).replace(/\/+$/, "");

export const authOptions: NextAuthOptions = {
  debug: true,
  providers: [
    CredentialsProvider({
      name: "Credentials",
      credentials: {
        email:    { label: "Email", type: "email" },
        password: { label: "Password", type: "password" },
      },
      async authorize(credentials) {
        try {
          const loginRes = await fetch(`${API_BASE}/api/login`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              email: credentials?.email,
              password: credentials?.password,
            }),
          });

          if (!loginRes.ok) {
            console.error("❌ Login failed:", loginRes.status);
            return null;
          }

          const { token } = await loginRes.json();

          const meRes = await fetch(`${API_BASE}/api/me`, {
            method: "GET",
            headers: {
              "Content-Type": "application/json",
              Authorization: `Bearer ${token}`,
            },
          });

          if (!meRes.ok) {
            console.error("❌ /me failed:", meRes.status);
            return null;
          }

          const user = await meRes.json();

          return {
            id: user.id?.toString() ?? user.email,
            name: user.name,
            email: user.email,
            image: null,
            accessToken: token,
          };
        } catch (err) {
          console.error("❌ authorize error:", err);
          return null;
        }
      },
    }),
  ],
  session: {
    strategy: "jwt",
  },
  callbacks: {
    async jwt({ token, user }) {
      if (user) {
        token.accessToken = (user as any).accessToken;
        token.name = user.name;
        token.email = user.email;
      }
      return token;
    },
    async session({ session, token }) {
      session.user = {
        name: token.name as string | undefined,
        email: token.email as string | undefined,
        image: null,
      };
      (session as any).accessToken = token.accessToken as string | undefined;
      return session;
    },
    async redirect({ url, baseUrl }) {
      if (url === baseUrl || url === `${baseUrl}/`) {
        return `${baseUrl}/dashboard`;
      }
      if (url.startsWith(baseUrl)) return url;
      return baseUrl;
    },
  },
  pages: {
    signIn: "/login",
  },
};

export default NextAuth(authOptions);