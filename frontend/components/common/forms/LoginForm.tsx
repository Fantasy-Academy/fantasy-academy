'use client';
import { signIn } from "next-auth/react";
import React, { useState } from 'react';
import InputField from '../InputField';
import Btn from '../Btn';
import LinkButton from '../LinkBtn';
import {loginValidation} from '../../../utils/loginValidation';

const LoginForm = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);

    async function handleFormSubmit(e: React.FormEvent) {
        e.preventDefault();

        // Clear any previous error
        setError(null);

        // Validate credentials before attempting sign-in
        const validationError = await loginValidation(email, password);
        if (validationError) {
            setError(validationError);
            return;
        }

        try {
            // Call signIn with credentials and disable automatic redirection
            const result = await signIn('credentials', {
                redirect: false,
                email,
                password,
            });

            if (result?.error) {
                setError(result.error); // Display error message if login fails
            } else {
                window.location.href = '/profile'; // Redirect manually on success
            }
        } catch (err) {
            console.error('Login failed:', err);
            setError('An unexpected error occurred. Please try again.');
        }
    }


    return (
        <form className="space-y-6" onSubmit={handleFormSubmit}>
            <InputField
                id="email"
                label="Email address"
                type="email"
                name="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                autoComplete="email"
                required
            />
            <div>
                <div className="flex flex-col gap-2">
                    <InputField
                        id="password"
                        label="Password"
                        type="password"
                        name="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        autoComplete="current-password"
                        required
                    />
                    <LinkButton link="/forgotPassword" text="Forgot password?" />
                </div>
            </div>
            {error && <p className="text-red-500">{error}</p>}
            <div>

                <Btn type="submit" text="Login" />

            </div>
        </form>
    );
};

export default LoginForm;