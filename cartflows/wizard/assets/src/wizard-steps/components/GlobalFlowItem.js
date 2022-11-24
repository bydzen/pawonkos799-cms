// SCSS.
import './GlobalFlowItem.scss';

function GlobalFlowItem( { item } ) {
	const { thumbnail_image_url, title, type } = item;

	return (
		<>
			{ 'pro' === type ? (
				<span className={ `wcf-item__type wcf-item__type--${ type }` }>
					{ type }
				</span>
			) : (
				''
			) }

			<div className="wcf-item__inner ">
				<div className="wcf-item__thumbnail-wrap">
					<div
						className="wcf-item__thumbnail"
						style={ {
							backgroundImage: `url("${ thumbnail_image_url }")`,
						} }
					></div>
				</div>
				<div className="wcf-item__heading-wrap">
					<div className="wcf-item__heading">{ title }</div>
				</div>
			</div>
		</>
	);
}

export default GlobalFlowItem;
