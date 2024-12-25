import React from 'react';
import InputField from '../InputField';
import Btn from '../Btn';

interface LoginFormProps {
    handleSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
}

const LoginForm: React.FC<LoginFormProps> = ({ handleSubmit }) => {
    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <InputField id="nickname" label="Nickname" type="text" autoComplete="nickname" required />
            <InputField id="email" label="Email address" type="email" autoComplete="email" required />
            <InputField id="password" label="Password" type="password" autoComplete="current-password" required />
            <InputField id="confirm-password" label="Confirm password" type="password" autoComplete="" required />
            <div>
                <Btn type='submit' text='Sign Up' className="bg-cyan-400"/>
            </div>
        </form>
    );
};

export default LoginForm;