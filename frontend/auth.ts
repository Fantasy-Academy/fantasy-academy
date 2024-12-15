import NextAuth from 'next-auth'
import CredentialsProvider from 'next-auth/providers/credentials';
import { getUserByEmail } from './data/users';


export const {
  handlers: { GET, POST },
  auth,
  signIn,
  signOut

} = NextAuth({
  secret: process.env.NEXTAUTH_SECRET,
  session: {
    strategy: 'jwt'
  },

  providers: [
    CredentialsProvider({
      async authorize(credentials) {
        if (credentials === null) return null;
        try {
          const user = getUserByEmail(credentials?.email);

          if (user) {
            const isMatch = user?.password === credentials?.password;

            if (isMatch) {
              return {
                id: user.id,
                name: user.name,
                email: user.email,
                rank: user.rank,
                rounds: user.rounds,
              }
            }
            else {
              throw new Error('Check your password')
            }
          }
          else {
            throw new Error('User not found');
          }

        } catch (error) {
          throw new Error(error instanceof Error ? error.message : String(error));
        }
      }
    }),
  ]
})