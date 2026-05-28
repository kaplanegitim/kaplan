<?php
/**
 * İletişim sayfa template'i.
 *
 * @package Kaplan
 */

get_header();

set_query_var('hero_title', __('<span>İletişime</span> geçin', 'kaplan'));
set_query_var('hero_sub',   __('Sorularınız, talepleriniz ve iş birliği önerileriniz için bize ulaşın.', 'kaplan'));
set_query_var('hero_crumb', __('İletişim', 'kaplan'));
set_query_var('hero_bg',    'hero/Resim-6.jpg');
get_template_part('template-parts/page-hero');
?>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <article class="contact-info">
                <div class="contact-info__icon"><i class="fa-solid fa-location-dot"></i></div>
                <h4><?php esc_html_e('Adres', 'kaplan'); ?></h4>
                <p>Varyap Meridian D Blok 68<br>Ataşehir / İstanbul</p>
            </article>
            <article class="contact-info">
                <div class="contact-info__icon"><i class="fa-solid fa-phone"></i></div>
                <h4><?php esc_html_e('Telefon', 'kaplan'); ?></h4>
                <p><a href="tel:+905309672366">+90 530 967 23 66</a></p>
            </article>
            <article class="contact-info">
                <div class="contact-info__icon"><i class="fa-solid fa-envelope"></i></div>
                <h4><?php esc_html_e('E-posta', 'kaplan'); ?></h4>
                <p><a href="mailto:bilgi@kaplanegitim.com">bilgi@kaplanegitim.com</a></p>
            </article>
        </div>

        <div class="form-layout" style="margin-top:1rem;">
            <div class="form-card">
                <span class="section-head__eyebrow" style="display:block; margin-bottom:.5rem;"><?php esc_html_e('Mesaj Gönder', 'kaplan'); ?></span>
                <h2 style="margin-bottom:1.5rem;"><?php esc_html_e('Size nasıl yardımcı olabiliriz?', 'kaplan'); ?></h2>
                <form class="kpl-form">
                    <input type="hidden" name="form_type" value="contact" />
                    <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
                        <input type="text" name="_kpl_website" tabindex="-1" autocomplete="off" />
                    </div>
                    <div class="form-grid">
                        <div class="form-field">
                            <label><?php esc_html_e('Ad Soyad', 'kaplan'); ?> <span class="req">*</span></label>
                            <input type="text" name="name" required />
                        </div>
                        <div class="form-field">
                            <label><?php esc_html_e('E-posta', 'kaplan'); ?> <span class="req">*</span></label>
                            <input type="email" name="email" required />
                        </div>
                        <div class="form-field form-field--full">
                            <label><?php esc_html_e('Konu', 'kaplan'); ?></label>
                            <input type="text" name="subject" />
                        </div>
                        <div class="form-field form-field--full">
                            <label><?php esc_html_e('Mesajınız', 'kaplan'); ?> <span class="req">*</span></label>
                            <textarea name="message" required></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <span class="form-note"><?php esc_html_e('Mesajınız bilgi@kaplanegitim.com adresine iletilecektir.', 'kaplan'); ?></span>
                        <button type="submit" class="btn btn--primary"><?php esc_html_e('Gönder', 'kaplan'); ?> <i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                    <div class="kpl-form__status" role="status" aria-live="polite"></div>
                </form>
            </div>

            <aside>
                <div class="info-card" style="margin-bottom:1.25rem;">
                    <h4><?php esc_html_e('Çalışma Saatleri', 'kaplan'); ?></h4>
                    <ul>
                        <li><i class="fa-regular fa-clock"></i> <?php esc_html_e('Pazartesi – Cuma · 09:00 – 18:00', 'kaplan'); ?></li>
                        <li><i class="fa-regular fa-calendar-xmark"></i> <?php esc_html_e('Hafta sonu · Kapalı', 'kaplan'); ?></li>
                    </ul>
                </div>
                <div class="info-card">
                    <h4><?php esc_html_e('Bültene Üye Ol', 'kaplan'); ?></h4>
                    <p style="font-size:.9rem; color:var(--c-muted); margin-bottom:.85rem;"><?php esc_html_e('Eğitim, makale ve içeriklerden haberdar olun.', 'kaplan'); ?></p>
                    <form class="newsletter" style="background:#fff;border:1px solid var(--c-line);" onsubmit="event.preventDefault();this.querySelector('.newsletter__msg').textContent='<?php echo esc_js(__('Teşekkürler!', 'kaplan')); ?>';">
                        <input type="email" placeholder="<?php esc_attr_e('E-posta adresiniz', 'kaplan'); ?>" required style="color:var(--c-ink);" />
                        <button type="submit" aria-label="<?php esc_attr_e('Abone Ol', 'kaplan'); ?>"><i class="fa-solid fa-paper-plane"></i></button>
                        <small class="newsletter__msg" style="color:var(--c-primary-700);"></small>
                    </form>
                </div>
            </aside>
        </div>

        <div class="map-frame" style="margin-top:3rem;">
            <iframe src="https://www.google.com/maps?q=Varyap%20Meridian%2C%20Ata%C5%9Fehir%2C%20%C4%B0stanbul&output=embed" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<?php
get_footer();
