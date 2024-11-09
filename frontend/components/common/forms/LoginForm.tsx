import React from 'react';
import InputField from '../InputField';
import LinkButton from '../LinkBtn';
import Btn from '../Btn';

interface LoginFormProps {
    handleSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
}

const LoginForm: React.FC<LoginFormProps> = ({ handleSubmit }) => {
    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <InputField id="email" label="Email address" type="email" autoComplete="email" required />
            <div>
                <div className="flex flex-col gap-2">
                    <InputField id="password" label="Password" type="password" autoComplete="current-password" required />
                    <LinkButton link="/forgotPassword" text="Forgot password?" />
                </div>
            </div>
            <div>
                <Btn type='submit' text='Login' />
            </div>
        </form>
    );
};

export default LoginForm;