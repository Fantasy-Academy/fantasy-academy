import NextAuth from 'next-auth'
import CredentialsProvider from 'next-auth/providers/credentials'

const authOptions = {
    session: {
        strategy: 'jwt' as const, // Ujistěte se, že používáte JWT strategii pro session
    },
    providers: [
        CredentialsProvider({
            name: 'Credentials',
            credentials: {
                email: { label: "Email", type: "email" },
                password: { label: "Password", type: "password" },
            },
            authorize: async (credentials) => {
                const { email, password } = credentials as { email: string; password: string };

                try {
                    const res = await fetch('http://localhost:8080/api/auth', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ email, password }),
                    });

                    const data = await res.json();

                    if (res.ok && data.token) {
                        // Vracíme token a další údaje do session
                        return { token: data.token, email: data.email, name: data.name };
                    }

                    return null;
                } catch (error) {
                    console.error("Error during authorization:", error);
                    return null;
                }
            },
        }),
    ],
    pages: {
        signIn: '/login',
    },
    callbacks: {
        async jwt({ token, user }) {
            // Pokud uživatel je přihlášen a má token, přidáme ho do JWT
            if (user) {
                token.token = user.token; // Přidáme token do JWT
                token.email = user.email;
                token.name = user.name;
            }
            return token;
        },
        async session({ session, token }) {
            // Přenášíme token do session (session.token)
            session.token = token.token;
            session.user.email = token.email;
            session.user.name = token.name;
            return session;
        },
    },
};

export default NextAuth(authOptions);