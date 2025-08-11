"use client";

import React from 'react';
import LoginForm from '../../components/common/forms/LoginForm';
import LinkBtn from '../../components/common/LinkBtn';
import Head from "next/head";

const Login = () => {
  return (
    <div className="flex flex-1 flex-col justify-center px-6 lg:px-8 py-12">
      <Head>
        <title>Login | Fantasy Academy</title>
      </Head>
      <div className="sm:mx-auto sm:w-full sm:max-w-sm text-center">
        <h2 className="mt-4 text-4xl font-bold tracking-tight text-charcoal font-sourceSans3">
          Welcome Back!
        </h2>
      </div>

      <div className="sm:mx-auto sm:w-full sm:max-w-sm mt-10">
        <LoginForm />

        <div className="flex flex-col w-fit h-fit justify-center text-sm text-coolGray mx-auto mt-10 text-center gap-2">
          <LinkBtn link="/forgotPassword" text="Forgot Password" />
          <div className='flex'>
            <span className="mr-1">Don&apos;t have an account?</span>
            <LinkBtn link="/signup" text="Sign Up" />
          </div>
        </div>
      </div>
    </div>
  );
};

export default Login;