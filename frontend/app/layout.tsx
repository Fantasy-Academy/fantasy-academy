import React, { ReactNode } from 'react';
import '../globals.css';

import Header from '../components/common/Header';

export default function RootLayout({ children }: { children: ReactNode }) {
    return (
        <html>
            <head>
                <title>Fantasy Academy</title>
            </head>
            <body>
                <Header />
                {children}
            </body>
        </html>
    );
}