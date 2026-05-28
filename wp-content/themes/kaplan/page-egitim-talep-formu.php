<?php
/**
 * Eğitim Talep Formu sayfa template'i.
 *
 * @package Kaplan
 */

get_header();

set_query_var('hero_title', __('Eğitim <span>talep formu</span>', 'kaplan'));
set_query_var('hero_sub',   __('Aşağıdaki formu doldurarak eğitim talebinde bulunabilirsiniz. En kısa sürede tarafınıza dönüş yapılacaktır.', 'kaplan'));
set_query_var('hero_crumb', __('Eğitim Talep Formu', 'kaplan'));
set_query_var('hero_bg',    'hero/Resim-9.jpg');
get_template_part('template-parts/page-hero');
?>

<section class="form-section">
    <div class="container">
        <div class="form-layout">
            <div class="form-card">
                <h2 style="margin-bottom: 1.5rem;"><?php esc_html_e('Bilgilerinizi paylaşın', 'kaplan'); ?></h2>
                <form class="kpl-form">
                    <input type="hidden" name="form_type" value="training" />
                    <div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
                        <input type="text" name="_kpl_website" tabindex="-1" autocomplete="off" />
                    </div>
                    <div class="form-grid">
                        <div class="form-field">
                            <label><?php esc_html_e('İsim', 'kaplan'); ?> <span class="req">*</span></label>
                            <input type="text" name="first_name" required />
                        </div>
                        <div class="form-field">
                            <label><?php esc_html_e('Soyisim', 'kaplan'); ?> <span class="req">*</span></label>
                            <input type="text" name="last_name" required />
                        </div>
                        <div class="form-field">
                            <label><?php esc_html_e('E-posta', 'kaplan'); ?> <span class="req">*</span></label>
                            <input type="email" name="email" required />
                        </div>
                        <div class="form-field">
                            <label><?php esc_html_e('Telefon', 'kaplan'); ?> <span class="req">*</span></label>
                            <input type="tel" name="phone" required />
                        </div>
                        <div class="form-field">
                            <label><?php esc_html_e('Şirket', 'kaplan'); ?></label>
                            <input type="text" name="company" />
                        </div>
                        <div class="form-field">
                            <label><?php esc_html_e('Pozisyon', 'kaplan'); ?></label>
                            <input type="text" name="position" />
                        </div>
                        <div class="form-field form-field--full">
                            <label><?php esc_html_e('Katılmak İstediğiniz Eğitim', 'kaplan'); ?> <span class="req">*</span></label>
                            <select name="training" required>
                                <option value=""><?php esc_html_e('Eğitim seçiniz...', 'kaplan'); ?></option>
                                <?php
                                $t_args = ['post_type' => 'kpl_training', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC'];
                                $t_q = function_exists('kpl_query_with_lang_fallback') ? kpl_query_with_lang_fallback($t_args) : new WP_Query($t_args);
                                while ($t_q->have_posts()) : $t_q->the_post();
                                    $dur = get_post_meta(get_the_ID(), '_kpl_duration', true);
                                    $label = get_the_title() . ($dur ? " ({$dur})" : '');
                                ?>
                                    <option value="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html($label); ?></option>
                                <?php endwhile; wp_reset_postdata(); ?>
                                <option value="<?php esc_attr_e('Diğer / Özel talep', 'kaplan'); ?>"><?php esc_html_e('Diğer / Özel talep', 'kaplan'); ?></option>
                            </select>
                        </div>
                        <div class="form-field form-field--full">
                            <label><?php esc_html_e('Mesajınız', 'kaplan'); ?></label>
                            <textarea name="message" placeholder="<?php esc_attr_e('Eğitim formatı, tarih veya özel istekleriniz hakkında detay paylaşabilirsiniz...', 'kaplan'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <span class="form-note">* <?php esc_html_e('işaretli alanlar zorunludur', 'kaplan'); ?></span>
                        <button type="reset" class="btn btn--ghost"><?php esc_html_e('Temizle', 'kaplan'); ?></button>
                        <button type="submit" class="btn btn--primary"><?php esc_html_e('Talebi Gönder', 'kaplan'); ?> <i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                    <div class="kpl-form__status" role="status" aria-live="polite"></div>
                </form>
            </div>

            <aside>
                <div class="info-card" style="margin-bottom: 1.25rem;">
                    <h4><?php esc_html_e('Süreç', 'kaplan'); ?></h4>
                    <ul>
                        <li><i class="fa-solid fa-1"></i> <span><?php esc_html_e('Form ile talebinizi iletin', 'kaplan'); ?></span></li>
                        <li><i class="fa-solid fa-2"></i> <span><?php esc_html_e('24 saat içinde geri dönüş', 'kaplan'); ?></span></li>
                        <li><i class="fa-solid fa-3"></i> <span><?php esc_html_e('İhtiyaca özel program tasarımı', 'kaplan'); ?></span></li>
                        <li><i class="fa-solid fa-4"></i> <span><?php esc_html_e('Eğitim tarihinin planlanması', 'kaplan'); ?></span></li>
                    </ul>
                </div>
                <div class="info-card">
                    <h4><?php esc_html_e('İletişim', 'kaplan'); ?></h4>
                    <ul>
                        <li><i class="fa-solid fa-phone"></i> +90 530 967 23 66</li>
                        <li><i class="fa-solid fa-envelope"></i> bilgi@kaplanegitim.com</li>
                        <li><i class="fa-solid fa-location-dot"></i> <?php esc_html_e('Varyap Meridian D Blok 68, Ataşehir', 'kaplan'); ?></li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php
get_footer();
