'use client';
import { useState, useEffect } from 'react';
import { signIn } from 'next-auth/react';
import { useRouter } from 'next/router';
import { useSession } from "next-auth/react";

import BackgroundWrapper from '../../../layouts/BackgroundWrapper';
import InputField from '../../../components/inputField/InputField';
import Btn from '../../../components/button/Btn';
import LinkButton from '../LinkBtn';

export default function LoginPage() {
    const router = useRouter();
    const { data: session } = useSession();

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);

        console.log("🔐 Pokus o přihlášení:", { email, password });

        const res = await signIn('credentials', {
            redirect: false,
            email,
            password,
        });

        console.log("🔁 Výsledek signIn:", res);

        if (res?.error) {
            setError('Invalid credentials');
        } else {
            router.push('/profile');
        }
        if (res?.error) {
            console.error("Chyba přihlášení:", res.error);
            setError(res.error === 'CredentialsSignin' ? 'Invalid credentials' : res.error);
        }
    };

    useEffect(() => {
        if (!session?.accessToken) return;

        const fetchData = async () => {
            try {
                const res = await fetch("http://localhost:8080/api/protected", {
                    headers: {
                        Authorization: `Bearer ${session.accessToken}`,
                    },
                });

                if (!res.ok) {
                    throw new Error(`Server responded with ${res.status}`);
                }

                const data = await res.json();
                console.log("Protected data:", data);

                // Můžeš uložit data do stavu nebo contextu
                // setProtectedData(data);
            } catch (err) {
                console.error("Error fetching protected data:", err);
                // setError("Unauthorized" nebo err.message);
            }
        };

        fetchData();
    }, [session]);
    console.log("Session", session);



    return (
        <BackgroundWrapper>
            <div className="flex items-center justify-center py-6 px-4">
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