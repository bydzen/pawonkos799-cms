import React, { useEffect, useCallback } from 'react';
import { useHistory } from 'react-router-dom';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../utils/StateProvider';
import CartFlowsLogo from '@WizardImages/cartflows-logo.svg';

function WelcomeStep() {
	const history = useHistory();
	const [ { action_button }, dispatch ] = useStateValue();

	const redirectNextStep = useCallback( () => {
		history.push( {
			pathname: 'index.php',
			search: `?page=cartflow-setup&step=page-builder`,
		} );
	}, [] );

	useEffect( () => {
		dispatch( {
			status: 'SET_NEXT_STEP',
			action_button: {
				button_text: __( 'Start Setup', 'cartflows' ),
				button_class: 'wcf-start-setup',
			},
		} );

		const startOnboardingEvent = document.addEventListener(
			'wcf-redirect-page-builder-step',
			function () {
				redirectNextStep();
			},
			false
		);

		return () => {
			document.removeEventListener(
				'wcf-redirect-page-builder-step',
				startOnboardingEvent
			);
		};
	}, [ redirectNextStep ] );

	return (
		<div className="wcf-container">
			<div className="wcf-row mt-16">
				<div className="bg-white rounded mx-auto px-11 py-14 drop-shadow-sm">
					<h1 className="wcf-step-heading">
						{ __( 'Welcome to', 'cartflows' ) }
					</h1>

					<div className="max-w-xs mx-auto p-5">
						<img src={ CartFlowsLogo } alt="CartFlows Logo" />
					</div>

					<p className="text-center overflow-hidden max-w-xl mt-2.5 mx-auto text-base text-[#1F2937]">
						{ __(
							"You're only minutes away from having a more profitable WooCommerce store! This short setup wizard will help you get started with CartFlows.",
							'cartflows'
						) }
					</p>

					<div className="mt-[40px] flex justify-center">
						<div
							className={ `wcf-wizard--button ${
								action_button.button_class
									? action_button.button_class
									: ''
							}` }
						>
							{ action_button.button_text }
							<svg
								xmlns="http://www.w3.org/2000/svg"
								className="w-5 mt-0.5 ml-1.5 fill-[#243c5a]"
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
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}

export default WelcomeStep;
