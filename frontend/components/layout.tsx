import React, { ReactNode } from 'react';
import Header from './common/Header';
import Footer from './common/Footer';

export default function Layout({ children }: { children: ReactNode }) {
  return (
    <>
      <Header />
      <main className="pt-16 bg-darkWhite min-h-screen">
        {children}
      </main>
      <Footer />
    </>
  );
}