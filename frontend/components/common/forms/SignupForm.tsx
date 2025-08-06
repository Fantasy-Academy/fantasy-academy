'use client';

import React, { useState } from 'react';
import { signIn } from 'next-auth/react'; // ← přidat
import { useRouter } from 'next/navigation'; // ← přidat
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

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        const { name, email, password, confirmPassword } = formData;

        if (password !== confirmPassword) {
            alert("Hesla se neshodují.");
            return;
        }

        const payload = { name, email, password };
        console.log("📦 Odesílám data k registraci:", payload);

        try {
            const res = await fetch('http://localhost:8080/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (res.ok) {
                // ✅ Registrace OK → přihlásit uživatele
                const loginRes = await signIn('credentials', {
                    redirect: false,
                    email,
                    password,
                });

                if (loginRes?.ok) {
                    router.push('/dashboard'); // nebo jinam dle potřeby
                } else {
                    alert("Registrace proběhla, ale přihlášení selhalo.");
                }
            } else {
                const error = await res.json();
                console.error("❌ Server error:", error);
                alert(error.message || "Registrace selhala.");
            }
        } catch (err) {
            console.error("❌ Síťová chyba:", err);
            alert("Nepodařilo se kontaktovat server.");
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <InputField
                id="name"
                name="name"
                label="Name"
                placeholder="Name..."
                type="text"
                value={formData.name}
                autoComplete=''
                onChange={handleChange}
                required
            />
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
            <div className="mt-6">
                <Btn type="submit">Sign Up</Btn>
            </div>
        </form>
    );
};

export default SignupForm;