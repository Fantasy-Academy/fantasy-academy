"use client";

import React from 'react';
import { useRouter } from 'next/navigation';
import SignupForm from '../../components/common/forms/SignupForm';
import LinkBtn from '../../components/common/LinkBtn';

const SignUp = () => {
    const router = useRouter();

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        // Handle signup logic here, e.g., send form data to an API

        // Redirect to the dashboard or another page after successful signup
        router.push('/dashboard');
    };

    return (
        <div className="flex min-h-full flex-1 flex-col justify-center px-6 py-8 lg:px-8">
            <div className="sm:mx-auto sm:w-full sm:max-w-sm text-center">
                <h2 className="mt-10 text-4xl font-bold tracking-tight text-charcoal font-sourceSans3">
                    Create New Account
                </h2>
            </div>

            <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <SignupForm handleSubmit={handleSubmit} />
                <div className="mt-10 flex justify-center text-sm text-coolGray">
                    <span className="mr-1">Already have an account?</span>
                    <LinkBtn link="/login" text="Login" />
                </div>
            </div>
        </div>
    );
};

export default SignUp;