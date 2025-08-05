import React, { useState } from 'react';
import InputField from '../../inputField/InputField';
import Btn from '../../button/Btn';

const LoginForm: React.FC = () => {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { id, value } = e.target;
        setFormData(prev => ({ ...prev, [id]: value }));
    };

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        const { name, email, password, confirmPassword } = formData;

        if (password !== confirmPassword) {
            alert("Hesla se neshodují.");
            return;
        }

        try {
            const res = await fetch('http://localhost:8080/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name, email, password }),
            });

            if (res.ok) {
                alert("✅ Uživatelský účet byl úspěšně vytvořen.");
                // případně redirect nebo vyprázdnění formuláře:
                setFormData({ name: '', email: '', password: '', confirmPassword: '' });
            } else {
                const error = await res.json();
                console.error("❌ Server error:", error);
                alert(error.message || "Registrace selhala.");
            }
        } catch (err) {
            console.error("❌ Network chyba:", err);
            alert("Nepodařilo se kontaktovat server.");
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <InputField id="name" name='name' autoComplete='' label="Name" placeholder="Name..." type="text" value={formData.name} onChange={handleChange} required />
            <InputField id="email" name='email' autoComplete='' label="Email address" placeholder="Email..." type="email" value={formData.email} onChange={handleChange} required />
            <InputField id="password" name='password' autoComplete='' label="Password" placeholder="Password..." type="password" value={formData.password} onChange={handleChange} required />
            <InputField id="confirmPassword" name='confirm_password' autoComplete='' label="Confirm password" placeholder="Confirm password..." type="password" value={formData.confirmPassword} onChange={handleChange} required />
            <div className='mt-6'>
                <Btn type="submit">Sign Up</Btn>
            </div>
        </form>
    );
};

export default LoginForm;