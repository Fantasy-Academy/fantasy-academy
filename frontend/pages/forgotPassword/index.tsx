'use client';

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import InputField from '../../components/inputField/InputField';
import Btn from '../../components/button/Btn';
import Head from "next/head";

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_URL || 'http://localhost:8080';

type Step = 'request' | 'reset';

const ForgotPasswordPage: React.FC = () => {
  const router = useRouter();

  const [step, setStep] = useState<Step>('request');

  // Krok 1
  const [email, setEmail] = useState('');
  // Krok 2
  const [code, setCode] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState<{ type: 'error' | 'success' | null; text: string }>({
    type: null,
    text: '',
  });
  const [errors, setErrors] = useState<{
    email?: string;
    code?: string;
    newPassword?: string;
    confirmPassword?: string;
  }>({});

  const resetFeedback = () => {
    setMessage({ type: null, text: '' });
    setErrors({});
  };

  const validatePassword = (pwd: string): string | null => {
    if (pwd.length < 5) return 'Password must be at least 5 characters.';
    if (!/[A-Z]/.test(pwd)) return 'Password must include at least 1 uppercase letter.';
    if (!/[0-9]/.test(pwd)) return 'Password must include at least 1 number.';
    return null;
  };

  const handleRequestCode = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    resetFeedback();

    const newErrors: typeof errors = {};
    if (!email.trim()) newErrors.email = 'Please enter your email.';

    if (Object.keys(newErrors).length) {
      setErrors(newErrors);
      return;
    }

    setLoading(true);
    try {
      const res = await fetch(`${API_BASE}/api/forgotten-password/request-reset-code`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email }),
      });

      if (res.status === 204) {
        setMessage({
          type: 'success',
          text: 'We sent you a reset code. Check your email and enter the code below.',
        });
        setStep('reset');
      } else {
        let detail = 'Failed to request reset code.';
        try {
          const payload = await res.json();
          detail = payload?.detail || detail;
        } catch { /* ignore */ }
        setMessage({ type: 'error', text: detail });
      }
    } catch {
      setMessage({ type: 'error', text: 'Network error. Please try again.' });
    } finally {
      setLoading(false);
    }
  };

  const handleResetPassword = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    resetFeedback();

    const newErrors: typeof errors = {};
    if (!code.trim()) newErrors.code = 'Please enter the code you received.';
    if (!newPassword.trim()) newErrors.newPassword = 'Please enter a new password.';
    if (!confirmPassword.trim()) newErrors.confirmPassword = 'Please confirm your new password.';

    const pwdErr = validatePassword(newPassword);
    if (!newErrors.newPassword && pwdErr) newErrors.newPassword = pwdErr;

    if (!newErrors.confirmPassword && newPassword !== confirmPassword) {
      newErrors.confirmPassword = 'Passwords do not match.';
    }

    if (Object.keys(newErrors).length) {
      setErrors(newErrors);
      return;
    }

    setLoading(true);
    try {
      // ✅ správný endpoint podle API
      const res = await fetch(`${API_BASE}/api/forgotten-password/reset`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code, newPassword }),
      });

      if (res.status === 204) {
        setMessage({ type: 'success', text: 'Password has been reset. Redirecting to login…' });
        setTimeout(() => router.push('/login'), 1200);
      } else {
        let detail = 'Password reset failed.';
        try {
          const payload = await res.json();
          if (payload?.violations?.[0]?.message) {
            detail = payload.violations[0].message;
          } else {
            detail = payload?.detail || detail;
          }
        } catch { /* ignore */ }
        setMessage({ type: 'error', text: detail });
      }
    } catch {
      setMessage({ type: 'error', text: 'Network error. Please try again.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-[60vh] flex items-center justify-center px-4">
      <Head>
        <title>Forgot password | Fantasy Academy</title>
      </Head>
      <div className="w-full max-w-md bg-white rounded shadow p-6">
        <h1 className="text-2xl font-bold text-charcoal mb-1">Forgot Password</h1>
        <p className="text-coolGray mb-6">
          {step === 'request'
            ? 'Enter your account email. We’ll send you a reset code.'
            : 'Enter the reset code from your email and set a new password.'}
        </p>

        {step === 'request' ? (
          <form onSubmit={handleRequestCode} noValidate>
            <div className="mb-4">
              <InputField
                id="email"
                name="email"
                label="Email address"
                placeholder="Email..."
                type="email"
                value={email}
                autoComplete="email"
                onChange={(e) => {
                  setEmail(e.target.value);
                  if (errors.email) setErrors((p) => ({ ...p, email: undefined }));
                  if (message.type) setMessage({ type: null, text: '' });
                }}
                required
              />
              {errors.email && <p className="mt-1 text-sm text-vibrantCoral">{errors.email}</p>}
            </div>

            {message.type && (
              <p className={`mb-4 text-sm ${message.type === 'error' ? 'text-vibrantCoral' : 'text-pistachio'}`}>
                {message.text}
              </p>
            )}

            <Btn type="submit">{loading ? 'Sending…' : 'Send reset code'}</Btn>
          </form>
        ) : (
          <form onSubmit={handleResetPassword} noValidate>
            <div className="mb-4">
              <InputField
                id="code"
                name="code"
                label="Reset code"
                placeholder="Enter the code you received…"
                type="text"
                value={code}
                autoComplete="one-time-code"
                onChange={(e) => {
                  setCode(e.target.value);
                  if (errors.code) setErrors((p) => ({ ...p, code: undefined }));
                  if (message.type) setMessage({ type: null, text: '' });
                }}
                required
              />
              {errors.code && <p className="mt-1 text-sm text-vibrantCoral">{errors.code}</p>}
            </div>

            <div className="mb-4">
              <InputField
                id="newPassword"
                name="newPassword"
                label="New password"
                placeholder="New password…"
                type="password"
                value={newPassword}
                autoComplete="new-password"
                onChange={(e) => {
                  setNewPassword(e.target.value);
                  if (errors.newPassword) setErrors((p) => ({ ...p, newPassword: undefined }));
                  if (message.type) setMessage({ type: null, text: '' });
                }}
                required
              />
              {errors.newPassword && <p className="mt-1 text-sm text-vibrantCoral">{errors.newPassword}</p>}
              <p className="mt-1 text-xs text-coolGray">
                Must be at least 5 characters, include 1 uppercase letter and 1 number.
              </p>
            </div>

            <div className="mb-4">
              <InputField
                id="confirmPassword"
                name="confirmPassword"
                label="Confirm new password"
                placeholder="Confirm new password…"
                type="password"
                value={confirmPassword}
                autoComplete="new-password"
                onChange={(e) => {
                  setConfirmPassword(e.target.value);
                  if (errors.confirmPassword) setErrors((p) => ({ ...p, confirmPassword: undefined }));
                  if (message.type) setMessage({ type: null, text: '' });
                }}
                required
              />
              {errors.confirmPassword && <p className="mt-1 text-sm text-vibrantCoral">{errors.confirmPassword}</p>}
            </div>

            {message.type && (
              <p className={`mb-4 text-sm ${message.type === 'error' ? 'text-vibrantCoral' : 'text-pistachio'}`}>
                {message.text}
              </p>
            )}

            <Btn type="submit">{loading ? 'Resetting…' : 'Reset password'}</Btn>

            <button
              type="button"
              className="block mt-3 text-sm text-charcoal hover:underline"
              onClick={() => {
                setStep('request');
                setCode('');
                setNewPassword('');
                setConfirmPassword('');
                resetFeedback();
              }}
            >
              Didn’t get a code? Send again
            </button>
          </form>
        )}
      </div>
    </div>
  );
};

export default ForgotPasswordPage;