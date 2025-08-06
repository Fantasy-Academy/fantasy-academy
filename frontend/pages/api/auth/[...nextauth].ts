import NextAuth from "next-auth";
import CredentialsProvider from "next-auth/providers/credentials";
import type { NextAuthOptions } from "next-auth";
import type { DefaultSession } from "next-auth";

export const authOptions: NextAuthOptions = {
  debug: true,
  providers: [
    CredentialsProvider({
      name: "Credentials",
      credentials: {
        email: { label: "Email", type: "email" },
        password: { label: "Password", type: "password" },
      },
      async authorize(credentials) {
        console.log("🔐 Pokus o přihlášení:", credentials);

        try {
          const loginRes = await fetch("http://api/api/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              email: credentials?.email,
              password: credentials?.password,
            }),
          });

          if (!loginRes.ok) {
            console.error("❌ Login selhal:", loginRes.status);
            return null;
          }

          const { token } = await loginRes.json();

          const meRes = await fetch("http://api/api/me", {
            method: "GET",
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          });

          if (!meRes.ok) {
            console.error("❌ /me selhalo:", meRes.status);
            return null;
          }

          const user = await meRes.json();
          console.log("✅ Uživatel načten:", user);

          return {
            id: user.id?.toString() ?? user.email,
            name: user.name,
            email: user.email,
            image: null, // přidej pokud máš obrázky
            accessToken: token,
          };
        } catch (err) {
          console.error("❌ Chyba v authorize:", err);
          return null;
        }
      }
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
        name: token.name,
        email: token.email,
        image: null,
      };
      (session as any).accessToken = token.accessToken;
      return session;
    },
  },
  pages: {
    signIn: "/login",
  },
};

export default NextAuth(authOptions);