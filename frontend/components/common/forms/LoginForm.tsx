'use client';
import { doCredentialLogin } from '../../../app/actions';
import React, { useState } from 'react';
import InputField from '../InputField';
import LinkButton from '../LinkBtn';
import Btn from '../../button/Btn';
import { useRouter } from 'next/navigation';


const LoginForm = () => {
    const router = useRouter();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);

    async function handleFormSubmit(e: React.FormEvent) {
        e.preventDefault();
        try {
            const formData = new FormData(e.currentTarget as HTMLFormElement);
            const response = await doCredentialLogin(formData);

            if (!!response.error){

            }else{
                router.push('/profile')
            }

        } catch (error) {
            console.error(e);
        }
    };

    

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

                <Btn text="Login" type="submit" />
            </div>
        </form>
    );
};

export default LoginForm;