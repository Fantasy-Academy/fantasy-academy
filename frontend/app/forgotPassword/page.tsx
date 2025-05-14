"use client";

import React from 'react';
import { useRouter } from 'next/navigation';
import InputField from '../../components/inputField/InputField';
import Button from '../../components/button/Btn';
import LinkBtn from '../../components/common/LinkBtn';

export default function ForgotPassword() {
  const router = useRouter();

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    alert("Password reset link has been sent to your email.");
    router.push('/login');
  };

  return (
    <div className="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-sm text-center">
        <h2 className="mt-10 text-4xl font-sourceSans3 font-bold tracking-tight text-charcoal">
          Forgot Password
        </h2>
      </div>

      <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form onSubmit={handleSubmit}>
          <InputField id="email" label="Email address" type="email" autoComplete="email" required placeholder="Email..." />
          <div className='mt-6'>
            <Button type="submit" text="Send Request" className="bg-vibrantCoral" />
          </div>
        </form>

        <div className="mt-10 flex justify-center text-sm text-coolGray">
          <span className="mr-1">Remembered your password?</span>
          <LinkBtn link="/login" text="Login" />
        </div>
      </div>
    </div>
  );
}