import { NextResponse } from 'next/server';
import { getToken } from 'next-auth/jwt';


export async function middleware(request: Request) {
    const secret = process.env.NEXTAUTH_SECRET;

    const token = await getToken({ req: request, secret });

    const { pathname } = new URL(request.url);

    if (token && (pathname === '/login' || pathname === '/signup')) {
        return NextResponse.redirect(new URL('/', request.url));
    }

    if (!token && pathname !== '/login' && pathname !== '/signup') {
        return NextResponse.redirect(new URL('/login', request.url));
    }

    return NextResponse.next();
}

export const config = {
    matcher: ['/profile', '/dashboard', '/login', '/signup']
};