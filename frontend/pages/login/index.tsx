"use client";

import React from 'react';
import LoginForm from '../../components/common/forms/LoginForm';
import LinkBtn from '../../components/common/LinkBtn';

const Login = () => {
  return (
    <div className="flex flex-1 flex-col justify-center px-6 lg:px-8 py-12">
      <div className="sm:mx-auto sm:w-full sm:max-w-sm text-center">
        <h2 className="mt-4 text-4xl font-bold tracking-tight text-charcoal font-sourceSans3">
          Welcome Back!
        </h2>
      </div>

      <div className="sm:mx-auto sm:w-full sm:max-w-sm">
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