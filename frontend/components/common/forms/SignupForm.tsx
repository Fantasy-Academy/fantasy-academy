'use client';

import React, { useState } from 'react';
import { signIn } from 'next-auth/react';
import { useRouter } from 'next/navigation';
import InputField from '../../inputField/InputField';
import Btn from '../../button/Btn';

const SignupForm: React.FC = () => {
    const router = useRouter();

    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
    });

    const [errors, setErrors] = useState<{ name?: string; email?: string; password?: string; confirmPassword?: string; general?: string }>({});
    const [loading, setLoading] = useState(false);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
        setErrors((prev) => ({ ...prev, [name]: undefined, general: undefined }));
    };

    const validatePassword = (password: string) => {
        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const minLength = password.length >= 5;
        return hasUpperCase && hasNumber && minLength;
    };

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        let newErrors: typeof errors = {};
        const { name, email, password, confirmPassword } = formData;

        if (!name.trim()) newErrors.name = 'Please fill name';
        if (!email.trim()) newErrors.email = 'Please fill email';
        if (!password) {
            newErrors.password = 'Please fill password';
        } else if (!validatePassword(password)) {
            newErrors.password = 'Password must be at least 5 characters long and contain at least 1 uppercase letter and 1 number.';
        }
        if (confirmPassword !== password) {
            newErrors.confirmPassword = 'Password is not matching.';
        }

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        setLoading(true);
        const payload = { name, email, password };

        try {
            const res = await fetch('http://localhost:8080/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (res.ok) {
                const loginRes = await signIn('credentials', {
                    redirect: false,
                    email,
                    password,
                });

                if (loginRes?.ok) {
                    router.push('/dashboard');
                } else {
                    setErrors({ general: 'Registrace proběhla, ale přihlášení selhalo.' });
                }
            } else {
                const error = await res.json();
                setErrors({ general: error.message || 'Registrace selhala.' });
            }
        } catch {
            setErrors({ general: 'Nepodařilo se kontaktovat server.' });
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} noValidate>
            <div className="mb-4">
                <InputField
                    id="name"
                    name="name"
                    label="Name"
                    placeholder="Name..."
                    type="text"
                    value={formData.name}
                    onChange={handleChange}
                    autoComplete=''
                    required
                />
                {errors.name && <p className="mt-1 text-sm text-vibrantCoral">{errors.name}</p>}
            </div>

            <div className="mb-4">
                <InputField
                    id="email"
                    name="email"
                    autoComplete="email"
                    label="Email address"
                    placeholder="Email..."
                    type="email"
                    value={formData.email}
                    onChange={handleChange}
                    required
                />
                {errors.email && <p className="mt-1 text-sm text-vibrantCoral">{errors.email}</p>}
            </div>

            <div className="mb-4">
                <InputField
                    id="password"
                    name="password"
                    autoComplete="new-password"
                    label="Password"
                    placeholder="Password..."
                    type="password"
                    value={formData.password}
                    onChange={handleChange}
                    required
                />
                {errors.password && <p className="mt-1 text-sm text-vibrantCoral">{errors.password}</p>}
            </div>

            <div className="mb-4">
                <InputField
                    id="confirmPassword"
                    name="confirmPassword"
                    label="Confirm password"
                    placeholder="Confirm password..."
                    type="password"
                    value={formData.confirmPassword}
                    onChange={handleChange}
                    autoComplete=''
                    required
                />
                {errors.confirmPassword && <p className="mt-1 text-sm text-vibrantCoral">{errors.confirmPassword}</p>}
            </div>

            {errors.general && <p className="mb-4 text-sm text-vibrantCoral">{errors.general}</p>}

            <div className="mt-6">
                <Btn type="submit">
                    {loading ? 'Verifing' : 'Sign Up'}
                </Btn>
            </div>
        </form>
    );
};

export default SignupForm;