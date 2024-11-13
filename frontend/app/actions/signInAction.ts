// signinaction.ts
"use server";

import { signIn } from "../../auth";

export const handleSignIn = async (formData: FormData) => {
    try {
        const response = await signIn("credentials", {
            redirect: false,
            email: formData.get("email"),
            password: formData.get("password"),
        });

        if (response?.ok) {
            console.log("Sign-in successful:", response);
        } else {
            console.error("Sign-in error:", response?.error);
        }
    } catch (error) {
        console.error("Unexpected sign-in error:", error);
    }
};