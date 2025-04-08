@applying_promotion_coupon
Feature: Denying usage of nonexistent promotion coupon
    In order to pay proper amount after using the promotion coupon
    As a Customer
    I want to have promotion coupon's discounts applied to my cart only if the given promotion coupon is valid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order
        And I am a logged in customer

    @api @ui @mink:chromedriver
    Scenario: Receiving no discount from nonexistent coupon
        Given I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        And I use coupon with code "SANTA2011"
        Then I should be notified that the coupon is invalid
        And my cart total should be "$100.00"
        And there should be no discount applied
