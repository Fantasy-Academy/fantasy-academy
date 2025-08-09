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

  const [errors, setErrors] = useState<{ email?: string; password?: string; general?: string }>({});
  const [loading, setLoading] = useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
    setErrors((prev) => ({ ...prev, [name]: undefined, general: undefined }));
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    let newErrors: typeof errors = {};

    if (!formData.email.trim()) {
      newErrors.email = 'Please fill Email';
    }
    if (!formData.password.trim()) {
      newErrors.password = 'Please fill Password';
    }

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    setLoading(true);

    const res = await signIn('credentials', {
      redirect: false,
      email: formData.email,
      password: formData.password,
    });

    setLoading(false);

    if (res?.ok) {
      router.push('/dashboard');
    } else {
      setErrors({ general: 'Incorrect password or email.' });
    }
  };

  return (
    <form onSubmit={handleSubmit} noValidate>
      <div className="mb-4">
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
        {errors.email && <p className="mt-1 text-sm text-vibrantCoral">{errors.email}</p>}
      </div>

      <div className="mb-4">
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
        {errors.password && <p className="mt-1 text-sm text-vibrantCoral">{errors.password}</p>}
      </div>

      {errors.general && (
        <p className="mb-4 text-sm text-vibrantCoral">{errors.general}</p>
      )}

      <div className="mt-6">
        <Btn type="submit">
          {loading ? 'Verifing...' : 'Log In'}
        </Btn>
      </div>
    </form>
  );
};

export default LoginForm;