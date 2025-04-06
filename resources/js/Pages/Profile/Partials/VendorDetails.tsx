import PrimaryButton from '@/Components/PrimaryButton';
import {useForm, usePage} from '@inertiajs/react';
import React, {FormEventHandler,useRef, useState} from 'react';
import SecondaryButton from '@/Components/SecondaryButton';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';

export default function VendorDetails(
    props: {
        className?: string;
    },
) {
    const [showBecomeVendorConfirmation, setShowBecomeVendorConfirmation] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const user = usePage().props.auth.user;
    const token = usePage().props.csrf_token;
    const {
        data,
        setData,
        post,
        processing,
        errors,
        recentlySuccessful,
    } =
        useForm({
            store_name: user.vendor?.store_name || user.name,
            store_address: user.vendor?.store_address,
        });

        const onStoreChangeName = (e: React.ChangeEvent<HTMLInputElement>) => {
            setData('store_name', e.target.value.toLowerCase().replace(/\s+/g, '-'));
        }

        const becomeVendor:FormEventHandler = (ev: React.FormEvent<HTMLFormElement>) => {
            ev.preventDefault();

            post(route('vendor.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    setSuccessMessage('You can now create and publish products');
                },
                onError: () => {
                    setSuccessMessage('Something went wrong');
                },
            })
        }

        const updateVendor:FormEventHandler = (ev: React.FormEvent<HTMLFormElement>) => {
            ev.preventDefault();

            post(route('vendor.update'), {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    setSuccessMessage('Your details have been updated');
                },
                onError: () => {
                    setSuccessMessage('Something went wrong');
                },

            })
        }

        const closeModal = () => {
            setShowBecomeVendorConfirmation(false);
        }

    return (
        <section className={props.className}>
            </section>
    );
}