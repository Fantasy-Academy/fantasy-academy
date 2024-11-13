import React, { useState } from 'react';
import InputField from '../InputField';
import LinkButton from '../LinkBtn';
import Btn from '../Btn';
import { useRouter } from 'next/navigation';
import { signIn } from "next-auth/react"; // Ujistěte se, že používáte správný import

const LoginForm = () => {
    const router = useRouter();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        console.log("Form submitted"); // Kontrolní výstup pro potvrzení odeslání formuláře

        const result = await signIn("credentials", {
            redirect: false,
            email,
            password,
        });

        if (result && result.error) {
            setError("Invalid credentials, please try again.");
            console.error(result.error); // Výpis chyby pro ladění
        } else {
            console.log("User signed in successfully.");
            router.push("/"); // Přesměrování po úspěšném přihlášení
        }
    };

    return (
        <form className="space-y-6" onSubmit={handleSubmit}>
            <InputField
                id="email"
                label="Email address"
                type="email"
                name="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                autoComplete="email"
                required
            />
            <div>
                <div className="flex flex-col gap-2">
                    <InputField
                        id="password"
                        label="Password"
                        type="password"
                        name="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        autoComplete="current-password"
                        required
                    />
                    <LinkButton link="/forgotPassword" text="Forgot password?" />
                </div>
            </div>
            {error && <p className="text-red-500">{error}</p>}
            <div>
                {/* Přidáváme type="submit" pro odeslání formuláře */}
                <Btn text="Login" type="submit" />
            </div>
        </form>
    );
};

export default LoginForm;