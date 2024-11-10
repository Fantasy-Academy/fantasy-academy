import React, { useState } from 'react';
import InputField from '../InputField';
import LinkButton from '../LinkBtn';
import Btn from '../Btn';
import { signIn } from 'next-auth/react';
import { useRouter } from 'next/navigation';

const LoginForm = () => {
    const [userInfo, setUserInfo] = useState({ email: '', password: '' });
    const router = useRouter();

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        console.log('Submitting form with', userInfo);

        const res = await signIn('credentials', {
            email: userInfo.email,
            password: userInfo.password,
            redirect: false,
        });

        console.log('SignIn response:', res);

        if (res?.error) {
            console.log('Authentication failed:', res.error);
        } else if (res?.ok) {
            console.log('Login successful:', res);
            router.push('/dashboard');
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <InputField
                id="email"
                value={userInfo.email}
                onChange={({ target }) => setUserInfo({ ...userInfo, email: target.value })}
                label="Email address"
                type="email"
                autoComplete="email"
                required
            />
            <div>
                <div className="flex flex-col gap-2">
                    <InputField
                        id="password"
                        value={userInfo.password}
                        onChange={({ target }) => setUserInfo({ ...userInfo, password: target.value })}
                        label="Password"
                        type="password"
                        autoComplete="current-password"
                        required
                    />
                    <LinkButton link="/forgotPassword" text="Forgot password?" />
                </div>
            </div>
            <div>
                <Btn type="submit" text="Login" />
            </div>
        </form>
    );
};

export default LoginForm;