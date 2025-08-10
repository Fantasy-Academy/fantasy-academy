// pages/api/auth/[...nextauth].ts
import NextAuth from "next-auth";
import CredentialsProvider from "next-auth/providers/credentials";
import type { NextAuthOptions } from "next-auth";
import type { DefaultSession } from "next-auth";

// Prefer server BACKEND_URL (např. "http://api" v docker-compose),
// fallback na veřejné NEXT_PUBLIC_BACKEND_URL, a nakonec lokál.
const API_BASE = (
  process.env.BACKEND_URL ||
  process.env.NEXT_PUBLIC_BACKEND_URL ||
  "http://localhost:8080"
).replace(/\/+$/, "");

export const authOptions: NextAuthOptions = {
  // DŮLEŽITÉ pro produkci (podepisování JWT/cookies)
  secret: process.env.NEXTAUTH_SECRET,

  // debug jen mimo produkci
  debug: process.env.NODE_ENV !== "production",

  providers: [
    CredentialsProvider({
      name: "Credentials",
      credentials: {
        email:    { label: "Email", type: "email" },
        password: { label: "Password", type: "password" },
      },
      async authorize(credentials) {
        try {
          // 1) login -> získat token
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

          // 2) /api/me -> data uživatele
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

          // NextAuth vyžaduje string id
          return {
            id: user.id?.toString?.() ?? user.email,
            name: user.name ?? user.username ?? user.email,
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
        (token as any).accessToken = (user as any).accessToken;
        token.name  = user.name  ?? token.name;
        token.email = user.email ?? token.email;
      }
      return token;
    },
    async session({ session, token }) {
      (session as any).accessToken = (token as any).accessToken as string | undefined;
      session.user = {
        name: token.name as string | undefined,
        email: token.email as string | undefined,
        image: null,
      } as DefaultSession["user"];
      return session;
    },
    // Po přihlášení pošli uživatele na /dashboard, pokud není explicitní callbackUrl
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