'use client';

import React, { useState } from 'react';
import { signIn } from 'next-auth/react';
import { useRouter } from 'next/navigation';
import InputField from '../../inputField/InputField';
import Btn from '../../button/Btn';

const LoginForm: React.FC = () => {
  const router = useRouter();

  const [formData, setFormData] = useState({
    email: '',
    password: '',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    const { email, password } = formData;

    const res = await signIn('credentials', {
      redirect: false,
      email,
      password,
    });

    if (res?.ok) {
      router.push('/dashboard'); // nebo jiná cílová stránka
    } else {
      alert("Přihlášení selhalo. Zkontroluj údaje.");
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <InputField
        id="email"
        name="email"
        label="Email address"
        placeholder="Email..."
        type="email"
        value={formData.email}
        autoComplete="email"
        onChange={handleChange}
        required
      />
      <InputField
        id="password"
        name="password"
        label="Password"
        placeholder="Password..."
        type="password"
        value={formData.password}
        autoComplete="current-password"
        onChange={handleChange}
        required
      />
      <div className="mt-6">
        <Btn type="submit">Log In</Btn>
      </div>
    </form>
  );
};

export default LoginForm;