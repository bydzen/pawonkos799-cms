import React from 'react';
import { __ } from '@wordpress/i18n';
import CfIcon from '@WizardImages/cartflows-icon.svg';
import { getExitSetupWizard } from '@Utils/Helpers';
import apiFetch from '@wordpress/api-fetch';
import { useLocation } from 'react-router-dom';

function Index() {
	const search = useLocation().search;
	const step = new URLSearchParams( search ).get( 'step' );

	const handleClick = ( e ) => {
		e.preventDefault();

		const ajaxData = new window.FormData();

		ajaxData.append( 'action', 'cartflows_onboarding_exit' );
		ajaxData.append( 'security', cartflows_wizard.onboarding_exit_nonce );
		ajaxData.append( 'current_step', step );

		apiFetch( {
			url: ajaxurl,
			method: 'POST',
			body: ajaxData,
		} ).then( ( response ) => {
			if ( response.success ) {
				window.location.href = getExitSetupWizard();
			}
		} );
	};

	return (
		<>
			<header className="wcf-setup-header bg-white shadow-md-1 fixed inset-x-0 z-50">
				<div className="max-w-full">
					<div className="flex justify-between h-[76px] ">
						<div className="flex border-r border-gray-300 px-7 py-5">
							<div className="flex-shrink-0 flex items-center">
								<img
									className="block lg:hidden h-8 w-auto"
									src={ CfIcon }
									alt="Workflow"
								/>
								<img
									className="hidden lg:block h-8 w-auto"
									src={ CfIcon }
									alt="Workflow"
								/>
							</div>
						</div>
						<div className="border-l border-gray-300 sm:flex sm:items-center">
							<div
								className="bg-white px-7 py-5 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none"
								title={ __( 'Exit Setup Wizard', 'cartflows' ) }
								onClick={ handleClick }
							>
								<svg
									className="h-6 w-6"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 24 24"
								>
									<path
										d="M10 6H6C4.89543 6 4 6.89543 4 8V18C4 19.1046 4.89543 20 6 20H16C17.1046 20 18 19.1046 18 18V14M14 4H20M20 4V10M20 4L10 14"
										stroke="currentColor"
										strokeLinecap="round"
										strokeLinejoin="round"
										strokeWidth={ 2 }
									/>
								</svg>
							</div>
						</div>
					</div>
				</div>
			</header>
		</>
	);
}
export default Index;
