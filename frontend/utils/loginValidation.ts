// utils/validateCredentials.ts
import { getUserByEmail } from '../data/users';

export async function loginValidation(email: string, password: string): Promise<string | null> {
    return new Promise((resolve) => {
        const user = getUserByEmail(email);

        if (!user || user.password !== password) {
            resolve("Invalid email or password!");
        }
        else {
            resolve(null);
        }
    });
}