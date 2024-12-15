import NextAuth from "next-auth";
import { DefaultSession } from "next-auth";

declare module "next-auth" {
  interface User {
    rank: string;
    rounds: string;
  }
  
  interface Session {
    user: User; 
  }
}