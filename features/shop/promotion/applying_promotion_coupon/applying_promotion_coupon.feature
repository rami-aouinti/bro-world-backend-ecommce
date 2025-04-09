@applying_promotion_coupon
Feature: Applying promotion coupon
    In order to pay proper amount after using the promotion coupon
    As a Customer
    I want to have promotion coupon's discounts applied to my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order
        And I am a logged in customer

    @api @ui
    Scenario: Receiving fixed discount for my cart
        Given I added product "PHP T-Shirt" to the cart
        And I applied the coupon with code "SANTA2016"
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"
