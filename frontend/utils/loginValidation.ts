export async function loginValidation(email: string, password: string): Promise<string | null> {
    if (!email || !password) {
        return "Please enter both email and password.";
    }

    return null;
}