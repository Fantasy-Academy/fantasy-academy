import React, { ReactNode } from 'react';
import SessionWrapper from '../components/SessionWrapper';
import '../globals.css';
import Header from '../components/common/Header';

export default function RootLayout({ children }: { children: ReactNode }) {
    return (
        <SessionWrapper>
            <html>
                <head>
                    <title>Fantasy Academy</title>
                </head>
                <body className='bg-slate-700'>
                    <Header />
                    <div className='pt-16'>{children}</div>
                </body>
            </html>
        </SessionWrapper>
    );
}