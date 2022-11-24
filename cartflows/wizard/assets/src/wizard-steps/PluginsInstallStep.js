import React, { useState, useEffect, useCallback } from 'react';
import { __ } from '@wordpress/i18n';
import { useHistory } from 'react-router-dom';
import { useStateValue } from '../utils/StateProvider';

function PluginsInstallStep() {
	const [ processing, setProcessing ] = useState( {
		isProcessing: false,
		buttonText: __( 'Install & Activate', 'cartflows' ),
	} );

	const { buttonText } = processing;

	const [ { action_button }, dispatch ] = useStateValue();
	const history = useHistory();

	/**
	 * Dispatcher function to change the button text on wizard footer.
	 */
	const dispatchChangeButtonText = useCallback( ( data ) => {
		dispatch( {
			status: 'SET_NEXT_STEP',
			action_button: data,
		} );
	}, [] );

	/**
	 * Function used to change the footer button text and the primary buttin text while processing the request.
	 */
	const handleOnClickProcessing = function () {
		const processing_buttonText = __(
			'Installing required plugins…',
			'cartflows'
		);

		setProcessing( {
			isProcessing: true,
			buttonText: processing_buttonText,
		} );

		dispatchChangeButtonText( {
			button_text: processing_buttonText,
			button_class: 'is-loading',
		} );

		dispatch( {
			status: 'PROCESSING',
		} );
	};

	useEffect( () => {
		dispatchChangeButtonText( {
			button_text: __( 'Install & Activate', 'cartflows' ),
			button_class: '',
		} );

		const installPluginsSuccess = document.addEventListener(
			'wcf-plugins-install-success',
			function () {
				setProcessing( false );
				history.push( {
					pathname: 'index.php',
					search: `?page=cartflow-setup&step=global-checkout`,
				} );

				dispatch( {
					status: 'RESET',
				} );
			},
			false
		);

		const installPluginsProcess = document.addEventListener(
			'wcf-install-require-plugins-processing',
			function () {
				handleOnClickProcessing();
			},
			false
		);

		return () => {
			document.removeEventListener(
				'wcf-plugins-install-success',
				installPluginsSuccess
			);
			document.removeEventListener(
				'wcf-install-require-plugins-processing',
				installPluginsProcess
			);
		};
	}, [ dispatchChangeButtonText ] );

	return (
		<div className="wcf-container">
			<div className="wcf-row mt-12">
				<div className="bg-white rounded mx-auto px-11 text-center py-14 drop-shadow-sm">
					<h1 className="wcf-step-heading flex justify-center items-center">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							className="h-8 w-8 align-middle text-2xl mr-1.5 fill-[#ffc83d]"
							viewBox="0 0 20 20"
							fill="currentColor"
						>
							<path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
						</svg>
						{ __(
							"Great job! Now let's install some required plugins.",
							'cartflows'
						) }
					</h1>
					<p className="mt-6 text-[#1F2937] text-base">
						{ __(
							'Since CartFlows uses WooCommerce, we will install it for you with Stripe for taking payments',
							'cartflows'
						) }
						<br />
						{ __( 'and Cart Abandonment Recovery.', 'cartflows' ) }
					</p>
					<p className="mt-6 leading-6">
						<span className="text-[#1F2937] text-base">
							{ __(
								'The following plugin will be installed and activated for you:',
								'cartflows'
							) }
						</span>
					</p>
					<div className="flex justify-center text-left text-base text-[#1F2937] mt-8 mx-auto">
						<div className="flex items-center flex-none w-48">
							<svg
								xmlns="http://www.w3.org/2000/svg"
								className="h-5 w-5 mr-2.5 fill-[#ED5A2E] float-left"
								viewBox="0 0 20 20"
								fill="currentColor"
							>
								<path
									fillRule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
									clipRule="evenodd"
								/>
							</svg>
							WooCommerce
						</div>
						<div className="flex items-center flex-1 w-32 max-w-[18rem]">
							<svg
								xmlns="http://www.w3.org/2000/svg"
								className="h-5 w-5 mr-2.5 fill-[#ED5A2E] float-left"
								viewBox="0 0 20 20"
								fill="currentColor"
							>
								<path
									fillRule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
									clipRule="evenodd"
								/>
							</svg>
							Cart Abandonment Recovery
						</div>
						<div className="flex items-center flex-1 w-32 max-w-[14rem]">
							<svg
								xmlns="http://www.w3.org/2000/svg"
								className="h-5 w-5 mr-2.5 fill-[#ED5A2E] float-left"
								viewBox="0 0 20 20"
								fill="currentColor"
							>
								<path
									fillRule="evenodd"
									d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
									clipRule="evenodd"
								/>
							</svg>
							Stripe Payment Gateway
						</div>
					</div>

					<div className="wcf-action-buttons mt-[40px] flex justify-center">
						<button
							className={ `install-required-plugins wcf-wizard--button ${
								action_button.button_class
									? action_button.button_class
									: ''
							}` }
						>
							{ buttonText }
						</button>
					</div>
				</div>
			</div>
		</div>
	);
}

export default PluginsInstallStep;
