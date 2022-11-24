import React from 'react';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../utils/StateProvider';
import { useHistory } from 'react-router-dom';

function FooterNavigationBar( props ) {
	const { previousStep, nextStep } = props;

	const [ { settingsProcess } ] = useStateValue();

	const history = useHistory();

	const handlePreviousStep = function () {
		if ( 'dashboard' === previousStep ) {
			window.location = cartflows_wizard.admin_base_url;
			return;
		}

		history.push( {
			pathname: 'index.php',
			search: `?page=cartflow-setup&step=` + previousStep,
		} );
	};

	const handleNextStep = function () {
		history.push( {
			pathname: 'index.php',
			search: `?page=cartflow-setup&step=` + nextStep,
		} );
	};

	return (
		<>
			<footer className="wcf-setup-footer bg-white shadow-md-1 fixed inset-x-0 bottom-0 h-[70px] z-10">
				<div className="flex items-center justify-between px-7 py-4 h-full">
					<div className="wcf-footer-left-section flex">
						<div
							className="flex-shrink-0 flex text-sm text-neutral-500 font-normal hover:text-orange-500 cursor-pointer"
							onClick={ handlePreviousStep }
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								className="w-5 mt-0.5 mr-1.5 fill-[#243c5a]"
								fill="none"
								viewBox="0 0 24 24"
								stroke="currentColor"
							>
								<path
									strokeLinecap="round"
									strokeLinejoin="round"
									strokeWidth={ 2 }
									d="M7 16l-4-4m0 0l4-4m-4 4h18"
								/>
							</svg>
							<button type="button">
								{ __( 'Back', 'cartflows' ) }
							</button>
						</div>
					</div>

					{ '' !== nextStep && (
						<div className="wcf-footer-right-section flex">
							{ 'processing' !== settingsProcess && (
								<button
									onClick={ handleNextStep }
									className="flex-shrink-0 flex text-sm text-neutral-500 font-normal hover:text-orange-500 cursor-pointer"
								>
									{ __( 'Skip', 'cartflows' ) }
								</button>
							) }
						</div>
					) }
				</div>
			</footer>
		</>
	);
}
export default FooterNavigationBar;
