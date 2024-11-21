'use server';
import { signIn, signOut } from '../../auth';

export async function doLogout() {
    await signOut({ redirectTo: '/login' });
}

export async function doCredentialLogin(formData: FormData) {
    try {
        const response = await signIn('credentials', {
            email: formData.get('email') as string,
            password: formData.get('password') as string,
            redirect: false
        });
        return response;
    } catch (error) {
        throw new Error(error instanceof Error ? error.message : 'An unexpected error occurred');
    }
}