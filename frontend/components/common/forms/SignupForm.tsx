import React from 'react';
import InputField from '../../inputField/InputField';
import Btn from '../../button/Btn';

interface LoginFormProps {
    handleSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
}

const LoginForm: React.FC<LoginFormProps> = ({ handleSubmit }) => {
    return (
        <form onSubmit={handleSubmit}>
            <InputField id="nickname" label="Nickname" placeholder="Nickname..." type="text" autoComplete="nickname" required />
            <InputField id="email" label="Email address" placeholder="Email..." type="email" autoComplete="email" required />
            <InputField id="password" label="Password" placeholder="Password..." type="password" autoComplete="current-password" required />
            <InputField id="confirm-password" label="Confirm password" placeholder="Confirm password..." type="password" autoComplete="" required />
            <div className='mt-6'>
                <Btn type="submit">Sign Up</Btn>
            </div>
        </form>
    );
};

export default LoginForm;