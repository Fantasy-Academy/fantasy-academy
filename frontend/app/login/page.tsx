"use client";

import React from 'react';
import LoginForm from '../../components/common/forms/LoginForm';
import LinkBtn from '../../components/common/LinkBtn';



const Login = () => {

  return (
    <div className="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
      <div className="sm:mx-auto sm:w-full sm:max-w-sm text-center">
        <h2 className="mt-10 text-4xl font-bold tracking-tight text-charcoal font-sourceSans3">
          Welcome Back!
        </h2>
      </div>

      <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <LoginForm  />
        <div className="mt-10 flex justify-center text-sm text-gray-500">
          <span className="mr-1">Don't have an account?</span>

        <LoginForm />

        <div className="mt-10 flex justify-center text-sm text-coolGray">
          <span className="mr-1">Don&apos;t have an account?</span>
          <LinkBtn link="/signup" text="Sign Up" />
        </div>
      </div>
    </div>
  );
};

export default Login;