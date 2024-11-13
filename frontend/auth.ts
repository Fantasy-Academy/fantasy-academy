import NextAuth from "next-auth";
import Credentials from "next-auth/providers/credentials";
import { signInSchema } from "./app/lib/zod";
import users from "./mockData/users"; // Import mock data

type User = {
  id: string;
  name: string; // nebo použijte username, pokud jste změnili typ
  email: string;
  password: string;
};

export const { handlers, signIn, signOut, auth } = NextAuth({
  providers: [
    Credentials({
      credentials: {
        email: { label: "Email", type: "text" },
        password: { label: "Password", type: "password" },
      },
      authorize: async (credentials): Promise<User | null> => {
        try {
          const { email, password } = await signInSchema.parseAsync(credentials);

          // Find user in mock data
          const user = users.find((u) => u.email === email && u.password === password);

          if (!user) {
            console.error("Invalid credentials.");
            return null;
          }

          console.log("User authenticated:", user);
          return user;
        } catch (error) {
          console.error("Authorization error:", error);
          return null;
        }
      },
    }),
  ],
  callbacks: {
    async session({ session, token }) {
      session.user = {
        ...session.user,
        id: token.id as string, // Explicitní přetypování na string
      };
      console.log("Session created with token:", token);
      return session;
    },
    async jwt({ token, user }) {
      if (user) {
        token.id = user.id as string; // Kontrola, že id je typu string
        token.email = user.email;
      }
      return token;
    },
  },
  pages: {
    signIn: "/login", // Nastavení vlastní přihlašovací stránky
  },
  secret: process.env.NEXTAUTH_SECRET,
});