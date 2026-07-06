/* =====================================================
   ABOUT
===================================================== */

.about-section {

    min-height: calc(100vh - 82px);

    padding: 90px 0;

    background: var(--off-white);

}


.about-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    align-items: center;

    gap: 70px;

}


.about-image {

    overflow: hidden;

    border-radius: 22px;

    box-shadow: var(--shadow-md);

}


.about-image img {

    width: 100%;

    height: 500px;

    object-fit: cover;

    transition: transform 0.6s ease;

}


.about-image:hover img {

    transform: scale(1.04);

}


.section-eyebrow {

    display: inline-block;

    margin-bottom: 12px;

    color: var(--primary-green);

    font-size: 17px;

    font-weight: 700;

}


.about-content h1 {

    margin-bottom: 24px;

    color: var(--text-primary);

    font-size: clamp(32px, 4vw, 48px);

    line-height: 1.35;

}


.about-content p {

    margin-bottom: 30px;

    color: var(--text-secondary);

    font-size: 14px;

    line-height: 2;

}


.service-feature-grid {

    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 16px;

}


.service-feature {

    min-height: 54px;

    padding: 14px 16px;

    display: flex;

    align-items: center;

    gap: 10px;

    background: var(--white);

    border-radius: 12px;

    box-shadow: var(--shadow-sm);

    font-size: 13px;

    transition: 0.25s;

}


.service-feature:hover {

    transform: translateY(-3px);

    box-shadow: var(--shadow-md);

}


.service-feature i {

    color: var(--primary-green);

    font-size: 18px;

}


.about-stat-grid {

    margin-top: 100px;

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;

}


.about-stat {

    padding: 30px;

    text-align: center;

    background: var(--white);

    border-radius: 16px;

    box-shadow: var(--shadow-sm);

}


.about-stat strong {

    display: block;

    color: var(--dark-green);

    font-size: 38px;

}


.about-stat span {

    color: var(--text-secondary);

    font-size: 13px;

}