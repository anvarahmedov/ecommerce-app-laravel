import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
        <Head title="Log in" />

        {status && (
            <div className="mb-4 text-sm font-medium text-green-600">
                {status}
            </div>
        )}

        {/* Login form card */}
        <form onSubmit={submit}>
            <div>
                <InputLabel htmlFor="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    name="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    autoComplete="username"
                    isFocused={true}
                    onChange={(e) => setData('email', e.target.value)}
                />

                <InputError message={errors.email} className="mt-2" />
            </div>

            <div className="mt-4">
                <InputLabel htmlFor="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    name="password"
                    value={data.password}
                    className="mt-1 block w-full"
                    autoComplete="current-password"
                    onChange={(e) => setData('password', e.target.value)}
                />

                <InputError message={errors.password} className="mt-2" />
            </div>

            <div className="mt-4 block">
                <label className="flex items-center">
                    <Checkbox
                        name="remember"
                        checked={data.remember}
                        onChange={(e) =>
                            setData('remember', e.target.checked)
                        }
                    />
                    <span className="ms-2 text-sm text-gray-600 dark:text-gray-400">
                        Remember me
                    </span>
                </label>
            </div>

            <div className="mt-4 flex justify-between items-center">
    {/* Wrapper for "Forgot your password?" */}
    <div className="flex justify-start">
        {canResetPassword && (
            <Link
                href={route('password.request')}
                className="link me-4"
            >
                Forgot your password?
            </Link>
        )}
    </div>

    {/* Wrapper for the button */}
    <div className="flex justify-end">
        <PrimaryButton className="ms-0" disabled={processing}>
            <p className='ms-5 me-5'>Login</p>
        </PrimaryButton>
    </div>
</div>
        </form>

        <div className="mt-4 text-center">
        <p className="text-sm text-gray-600">
            Don't have an account?{' '}
            <a
                href="/register"
                className="text-indigo-600 hover:text-indigo-800 font-semibold"
            >
                Sign Up
            </a>
        </p>
    </div>

        {/* "Don't have an account yet?" link outside and below the login form */}

    </GuestLayout>




    );
}
