'use client';
import { useState } from 'react';
import { signIn } from 'next-auth/react';
import { useRouter } from 'next/router';

import BackgroundWrapper from '../../../layouts/BackgroundWrapper';
import InputField from '../../../components/inputField/InputField';
import Btn from '../../../components/button/Btn';
import LinkButton from '../LinkBtn';

export default function LoginPage() {
    const router = useRouter();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        const res = await signIn('credentials', {
            redirect: false,
            email,
            password,
        });

        if (res?.error) {
            setError('Invalid credentials');
        } else {
            router.push('/profile'); // nebo "/dashboard", podle toho, jak to máš
        }
    };

    return (
        <BackgroundWrapper>
            <div className="flex items-center justify-center min-h-screen px-4">
                <form
                    onSubmit={handleLogin}
                    className="bg-white shadow-md rounded px-8 pt-6 pb-8 w-full max-w-md"
                >
                    <h2 className="text-3xl font-bold mb-6 text-center text-vibrantCoral">Login</h2>

                    <InputField
                        id="email"
                        label="Email"
                        name="email"
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                        placeholder="you@example.com"
                        error=""
                        autoComplete="email" 
                    />

                    <div className="mt-4">
                        <InputField
                            id="password"
                            label="Password"
                            name="password"
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                            placeholder="••••••••"
                            error=""
                            autoComplete="current-password"
                        />
                    </div>

                    {error && <p className="text-vibrantCoral mt-2">{error}</p>}

                    <div className="mt-4">
                        <LinkButton link="/forgotPassword" text="Forgot password?" />
                    </div>

                    <div className="mt-6">
                        <Btn type="submit">Sign In</Btn>
                    </div>
                </form>
            </div>
        </BackgroundWrapper>
    );
}