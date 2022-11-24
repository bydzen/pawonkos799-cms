import React, { useState, useEffect, useCallback } from 'react';
import { useHistory } from 'react-router-dom';
import { sprintf, __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import ReactHtmlParser from 'react-html-parser';
import { useStateValue } from '../utils/StateProvider';

function OptinStep() {
	const history = useHistory();

	const [ requestError, setRequestError ] = useState( false );
	const [ { action_button }, dispatch ] = useStateValue();

	const [ formErrors, setFormErrors ] = useState( {
		name_error_message: '',
		email_error_message: '',
		error_class: 'border-red-400',
	} );

	const { name_error_message, email_error_message, error_class } = formErrors;

	const changeOptinButtonText = useCallback( ( data ) => {
		dispatch( {
			status: 'SET_NEXT_STEP',
			action_button: data,
		} );
	}, [] );

	const default_button_text = __( 'Continue', 'cartflows' );

	useEffect( () => {
		// Set Foooter button text.
		changeOptinButtonText( {
			button_text: default_button_text,
			button_class: 'wcf-enrol-optin',
		} );
	}, [ changeOptinButtonText ] );

	const validateForm = function ( posted_data ) {
		let is_errors = false;
		const errors = {};

		if ( ! posted_data.name || '' === posted_data.name ) {
			errors.name_error_message = __( 'Please Enter Name', 'cartflows' );
			is_errors = true;
		}

		if ( ! posted_data.email || '' === posted_data.email ) {
			errors.email_error_message = __(
				'Please Enter Email ID',
				'cartflows'
			);
			is_errors = true;
		} else if (
			! /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(
				posted_data.email
			)
		) {
			errors.email_error_message = __(
				'Entered email address is not a valid email',
				'cartflows'
			);
			is_errors = true;
		}

		if ( is_errors ) {
			setFormErrors( {
				name_error_message: errors.name_error_message
					? errors.name_error_message
					: '',
				email_error_message: errors.email_error_message
					? errors.email_error_message
					: '',
			} );
		}

		return is_errors;
	};

	const submitOptinForm = function ( e ) {
		e.preventDefault();

		changeOptinButtonText( {
			button_text: __( 'Processing', 'cartflows' ),
			button_class: 'wcf-enrol-optin',
		} );

		dispatch( {
			status: 'PROCESSING',
		} );

		const ajaxData = new window.FormData();

		ajaxData.append( 'action', 'cartflows_user_onboarding' );
		ajaxData.append( 'security', cartflows_wizard.user_onboarding_nonce );

		const name = document.getElementById( 'wcf-user-name' ).value;
		const email = document.getElementById( 'wcf-user-email' ).value;

		if ( validateForm( { name, email } ) ) {
			changeOptinButtonText( {
				button_text: default_button_text,
				button_class: 'wcf-enrol-optin',
			} );

			dispatch( {
				status: 'RESET',
			} );
			return;
		}

		ajaxData.append( 'user_fname', name );
		ajaxData.append( 'user_email', email );

		apiFetch( {
			url: ajaxurl,
			method: 'POST',
			body: ajaxData,
		} ).then( ( response ) => {
			if ( response.data.success ) {
				history.push( {
					pathname: 'index.php',
					search: `?page=cartflow-setup&step=ready`,
				} );
				dispatch( {
					status: 'RESET',
				} );
			} else {
				setRequestError( response.data.message );
			}

			changeOptinButtonText( {
				button_text: default_button_text,
				button_class: 'wcf-enrol-optin',
			} );
		} );
	};

	return (
		<div className="wcf-container">
			<div className="wcf-row mt-12">
				<div className="bg-white rounded text-center mx-auto px-11 py-14 drop-shadow-sm">
					<h1 className="wcf-step-heading">
						{ ReactHtmlParser(
							sprintf(
								/* translators: %s: html tag*/
								__(
									"One last step. %s Let's setup email reports on how your store is doing.",
									'cartflows'
								),
								'<br />'
							)
						) }
					</h1>
					<p className="mt-4 text-[#4B5563] text-base">
						{ ReactHtmlParser(
							sprintf(
								/* translators: %1$s: html tag, %2$s: html tag*/
								__(
									'Let CartFlows take the guesswork out of your checkout results. Each week your store will send %1$s you an email report with key metrics and insights. You also will receive emails from us to %2$s help your store sell more.',
									'cartflows'
								),
								'<br />',
								'<br />'
							)
						) }
					</p>
					<form action="#" className="max-w-2xl mx-auto mt-[40px]">
						<div className="wcf-form-fields sm:flex gap-x-5 text-left">
							<div className="w-full">
								<label
									htmlFor="wcf-user-name"
									className="sr-only"
								>
									{ __( 'Name', 'cartflows' ) }
								</label>
								<input
									id="wcf-user-name"
									type="text"
									className={ `wcf-input ${
										error_class ? error_class : ''
									}` }
									placeholder={ __(
										'Enter Your Name',
										'cartflows'
									) }
									defaultValue={
										cartflows_wizard.current_user_name
									}
								/>
								{ name_error_message && (
									<span className="text-red-700 px-2 text-sm">
										{ name_error_message }
									</span>
								) }
							</div>
							<div className="w-full">
								<label
									htmlFor="wcf-user-email"
									className="sr-only"
								>
									{ __( 'Email address', 'cartflows' ) }
								</label>
								<input
									id="wcf-user-email"
									type="email"
									className={ `wcf-input ${
										error_class ? error_class : ''
									}` }
									placeholder={ __(
										'Enter Your Email',
										'cartflows'
									) }
									defaultValue={
										cartflows_wizard.current_user_email
									}
								/>
								{ email_error_message && (
									<span className="text-red-700 px-2 text-sm">
										{ email_error_message }
									</span>
								) }
							</div>
						</div>

						<div className="wcf-action-buttons mt-[40px] grid justify-center">
							{ requestError && (
								<span className="text-red-700 mb-2 text-sm block">
									{ ' ' }
									{ requestError }{ ' ' }
								</span>
							) }
							<button
								onClick={ submitOptinForm }
								className={ `wcf-wizard--button ${
									action_button.button_class
										? action_button.button_class
										: ''
								}` }
							>
								{ action_button.button_text }
								<svg
									xmlns="http://www.w3.org/2000/svg"
									className="w-5 ml-1.5 fill-[#243c5a]"
									fill="none"
									viewBox="0 0 24 24"
									stroke="currentColor"
									strokeWidth={ 2 }
								>
									<path
										strokeLinecap="round"
										strokeLinejoin="round"
										d="M17 8l4 4m0 0l-4 4m4-4H3"
									/>
								</svg>
							</button>
						</div>
					</form>
					{ /* <p className="wcf-tc-statement mt-6 text-sm text-[#4B5563]">
						{ __(
							'By clicking "Notify me", you agree to receive our newsletters as part of this course.',
							'cartflows'
						) }
					</p> */ }
					{ /* { 'processing' !== settingsProcess && (
						<p className="mt-6 flex justify-center">
							<button
								onClick={ handleNextStep }
								className="p-2 font-normal text-gray-300 hover:text-gray-500 cursor-pointer"
							>
								{ __( 'Maybe Later', 'cartflows' ) }
							</button>
						</p>
					) } */ }
				</div>
			</div>
		</div>
	);
}

export default OptinStep;
